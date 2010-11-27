// InsteonSerial.cpp : Defines the entry point for the console application.
//

#include "stdafx.h"

void usage()
{
	printf("usage: InsteonSerial.exe <PORT>\n");
	printf("       PORT - Name of the serial port connected to the IM e.g. COM7\n");
}

int _tmain(int argc, _TCHAR* argv[])
{
	HANDLE hPort = NULL;
	OVERLAPPED ReadStatus = {0};
	DWORD BytesTransferred = 0;
	DWORD RecvByte = 0;
	PBYTE SendBytes = NULL;
	DWORD BytesToSend = 0;
	int i = 0;

	if (argc < 2)
	{
		usage();
		goto cleanup;
	}

	hPort = CreateFile(argv[1], 
					   GENERIC_READ | GENERIC_WRITE, 
                       0, 
                       0, 
                       OPEN_EXISTING,
                       FILE_ATTRIBUTE_NORMAL, //FILE_FLAG_OVERLAPPED,
                       0);

	if (NULL == hPort || INVALID_HANDLE_VALUE == hPort)
	{
		printf("Error opening port %S:  %d\n", argv[1], GetLastError());
		goto cleanup;
	}

	printf("Connected to port %S\n", argv[1]);

	if (argc > 2)
	{
		printf("Sending bytes\n");
		BytesToSend = argc - 2;
		SendBytes = ((PBYTE)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, BytesToSend));
		if (NULL == SendBytes)
		{
			printf("Error allocating SendBytes array\n");
			goto cleanup;
		}

		for (i=0; i < BytesToSend; i++)
		{
			//SendBytes[i] = _wtoi(argv[i+2]);
			swscanf_s(argv[i+2], L"%x", &(SendBytes[i]));
			//printf(" %d", SendBytes[i]);
		}
		printf("\nto the port.\n");

		if (!WriteFile(hPort, SendBytes, BytesToSend, &BytesTransferred, NULL))
		{
			printf("Error writing to port:  %d\n", GetLastError());
			goto cleanup;
		}

		printf("Message delivered to port.\n");
	}

	ReadStatus.hEvent = CreateEvent(NULL, FALSE, FALSE, NULL);
	if (NULL == ReadStatus.hEvent)
	{
		printf("Error creating read completion event.\n");
	}

	while(true)
	{
		//printf("Press a key to read a byte from the port.\n");
		//getchar();

		if (!ReadFile(hPort, &RecvByte, 1, &BytesTransferred, NULL))
		{
			printf("Error reading from port:  %d\n", GetLastError());
			goto cleanup;
		}

		printf("Byte '%x' received from port\n", RecvByte);
	}



cleanup:
	if (NULL != hPort && INVALID_HANDLE_VALUE != hPort)
	{
		CloseHandle(hPort);
	}

	if (NULL != SendBytes)
	{
		HeapFree(GetProcessHeap(), 0, SendBytes);
	}

	//getchar();

	return 0;

}

