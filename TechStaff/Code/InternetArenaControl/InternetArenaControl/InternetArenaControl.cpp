// InternetArenaControl.cpp : Defines the entry point for the console application.
//

#include "stdafx.h"

DWORD WINAPI SignalThread(LPVOID lpParameter)
{
	HANDLE hEndEvent = ((HANDLE)lpParameter);

	printf("Press a key to exit.\n");
	getchar();
	SetEvent(hEndEvent);
	return 0;
}

int _tmain(int argc, _TCHAR* argv[])
{
	BOOL bResults = FALSE;
	DWORD dwWaitStatus = WAIT_TIMEOUT;
	HANDLE hEndEvent = NULL;
	HANDLE hSignalThread = NULL;
    HINTERNET hSession = NULL;
    HINTERNET hConnect = NULL;
    HINTERNET hRequest = NULL;
	DWORD dwSize = 0;
    DWORD dwDownloaded = 0;
    LPSTR pszOutBuffer = NULL;
	int Command = 0;
	int param[2] = {0};

	if (!InitializeDevices(L"InsteonConfig.txt"))
	{
		printf("Error Initializing Insteon Devices:  %d\n", GetLastError());
		goto cleanup;
	}

	hEndEvent = CreateEvent(NULL, FALSE, FALSE, NULL);
	if (NULL == hEndEvent)
	{
		printf("Error creating end event:  %d\n", GetLastError());
		goto cleanup;
	}

	hSignalThread = CreateThread(NULL, 0, SignalThread, hEndEvent, 0, NULL);
	if (NULL == hSignalThread)
	{
		printf("Error creating signal thread:  %d\n", GetLastError());
		goto cleanup;
	}

    hSession = WinHttpOpen(L"Internet Arena Control", 
                           WINHTTP_ACCESS_TYPE_DEFAULT_PROXY,
                           WINHTTP_NO_PROXY_NAME, 
                           WINHTTP_NO_PROXY_BYPASS, 
						   0);

    if (NULL == hSession)
	{
		printf("Error opening WinHttp session:  %d\n", GetLastError());
		goto cleanup;
	}
        
	hConnect = WinHttpConnect(hSession, 
		                      L"www.reclinathon.com",
                              INTERNET_DEFAULT_HTTP_PORT, 
							  0);

    if (NULL == hConnect)
	{
		printf("Error connecting to reclinathon.com:  %d\n", GetLastError());
		goto cleanup;
	}

	while(dwWaitStatus != WAIT_OBJECT_0)
	{
   
		hRequest = WinHttpOpenRequest(hConnect, 
		                              L"GET", 
                                      L"/rtt/InternetArenaControl.php", 
	                                  NULL, 
									  WINHTTP_NO_REFERER, 
	                                  WINHTTP_DEFAULT_ACCEPT_TYPES,
	                                  WINHTTP_FLAG_REFRESH);

	    if (NULL == hRequest)
		{
			printf("Error opening http request:  %d\n", GetLastError());
			goto cleanup;
		}

	    bResults = WinHttpSendRequest(hRequest, 
	                                  WINHTTP_NO_ADDITIONAL_HEADERS,
	                                  0, 
									  WINHTTP_NO_REQUEST_DATA, 
									  0, 
	                                  0, 
									  0);

	    if (!bResults)
		{
			printf("Error sending http request:  %d\n", GetLastError());
		    goto cleanup;
		}

		bResults = WinHttpReceiveResponse(hRequest, NULL);

	    if (!bResults)
		{
			printf("Error receiving http response:  %d\n", GetLastError());
			goto cleanup;
		}

	    dwSize = 0;
	    if (!WinHttpQueryDataAvailable(hRequest, &dwSize))
		{
			printf("Error querying http data size:  %d\n", GetLastError());
		}

	    pszOutBuffer = ((LPSTR)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, dwSize+1));
        if (NULL == pszOutBuffer)
        {
			printf("Error allocating memory\n");
            goto cleanup;
        }

        if (!WinHttpReadData(hRequest, 
			                 (LPVOID)pszOutBuffer, 
                             dwSize, 
							 &dwDownloaded))
		{
			printf("Error reading http data:  %d\n", GetLastError());
			goto cleanup;
		}

		if (3 != sscanf_s(pszOutBuffer, "%d %d %d", &Command, &param[0], &param[1]))
		{
			printf("Malformed Command.\n");
			Command = 0;
		}

		switch(Command)
		{
		    case 0:
				dwWaitStatus = WaitForSingleObject(hEndEvent, 5000);
				break;
		    case 1:
				ControlDevice(param[0], param[1]);
				break;
			default:
				printf("Illegal Command:  %d\n", Command);
		}
        
		HeapFree(GetProcessHeap(), 0, pszOutBuffer);
		pszOutBuffer = NULL;

		WinHttpCloseHandle(hRequest);
		hRequest = NULL;

	}

cleanup:

    if (NULL != hRequest)
	{
		WinHttpCloseHandle(hRequest);
	}

    if (NULL != hConnect)
	{
		WinHttpCloseHandle(hConnect);
	}

    if (NULL != hSession)
	{
		WinHttpCloseHandle(hSession);
	}

	if (NULL != pszOutBuffer)
	{
		HeapFree(GetProcessHeap(), 0, pszOutBuffer);
	}

	if (NULL != hSignalThread)
	{
		CloseHandle(hSignalThread);
	}

	if (NULL != hEndEvent)
	{
		CloseHandle(hEndEvent);
	}

	return 0;
}

