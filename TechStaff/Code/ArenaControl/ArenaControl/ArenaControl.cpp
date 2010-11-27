// ArenaControl.cpp : Defines the exported functions for the DLL application.

#include "stdafx.h"

#define MAX_PORTNAME_LENGTH 10
#define MAX_DEVICES 25

//
// ArenaControl Global Variables
//

WCHAR szPortName[MAX_PORTNAME_LENGTH] = {0};
DEVICE_ADDRESS DeviceMap[MAX_DEVICES] = {0};
DWORD NumDevices = 0;

BOOL
InitializeDevices(
    IN LPCWSTR lpConfigFile
	)
{
	HANDLE hConfigFile = NULL;
	DWORD dwFileSize = INVALID_FILE_SIZE;
	DWORD dwConfigFileLength = 0;
	DWORD dwBytesRead = 0;
	DWORD dwErr = ERROR_INVALID_FUNCTION;
	BOOL bRet = FALSE;
	LPWSTR szConfigFile = NULL;
	LPWSTR szWorkingBuffer = NULL;
	DWORD i = 0;
	DWORD j = 0;
	BOOL PortToken = FALSE;
	int DeviceToken = 0;

	ZeroMemory(szPortName, MAX_PORTNAME_LENGTH * sizeof(WCHAR));
	ZeroMemory(DeviceMap, MAX_DEVICES * sizeof(DEVICE_ADDRESS));
	NumDevices = 0;

	hConfigFile = CreateFile(lpConfigFile,
		                     GENERIC_READ,
							 FILE_SHARE_READ,
							 NULL,
							 OPEN_EXISTING,
							 FILE_ATTRIBUTE_NORMAL,
							 NULL);
	if (NULL == hConfigFile)
	{
		dwErr = GetLastError();
		goto cleanup;
	}

	dwFileSize = GetFileSize(hConfigFile, NULL);
	if (INVALID_FILE_SIZE == dwFileSize)
	{
		dwErr = GetLastError();
		goto cleanup;
	}

	szConfigFile = ((LPWSTR)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, dwFileSize + sizeof(WCHAR)));
	if (NULL == szConfigFile)
	{
		dwErr = ERROR_NOT_ENOUGH_MEMORY;
		goto cleanup;
	}

	szWorkingBuffer = ((LPWSTR)HeapAlloc(GetProcessHeap(), HEAP_ZERO_MEMORY, dwFileSize + sizeof(WCHAR)));
	if (NULL == szWorkingBuffer)
	{
		dwErr = ERROR_NOT_ENOUGH_MEMORY;
		goto cleanup;
	}

	dwConfigFileLength = dwFileSize / sizeof(WCHAR);

	if (!ReadFile(hConfigFile, szConfigFile, dwFileSize, &dwBytesRead, NULL))
	{
		dwErr = GetLastError();
		goto cleanup;
	}

	for (i=0; i <= dwConfigFileLength; i++)
	{
		if (!IsCharAlphaNumeric(szConfigFile[i]))
		{
			continue;
		}

		ZeroMemory(szWorkingBuffer, dwFileSize + sizeof(WCHAR));
		for (j=0; (i+j) <= dwConfigFileLength; j++)
		{
			if (IsCharAlphaNumeric(szConfigFile[i+j]))
			{
				szWorkingBuffer[j] = szConfigFile[i+j];
			}
			else
			{	
				if (lstrcmpi(szWorkingBuffer, L"PORT") == 0)
				{
					PortToken = TRUE;
				}
				else if (lstrcmpi(szWorkingBuffer, L"DEV") == 0)
				{
					DeviceToken = 1;
				}
				else if (PortToken)
				{
					if (FAILED(StringCchCopy(szPortName, MAX_PORTNAME_LENGTH, szWorkingBuffer)))
					{
						dwErr = ERROR_INVALID_DATA;
						goto cleanup;
					}
					PortToken = FALSE;
				}
				else if (1 == DeviceToken)
				{
					if (1 != swscanf_s(szWorkingBuffer, L"%x", &(DeviceMap[NumDevices].DeviceID)))
					{
						dwErr = ERROR_INVALID_DATA;
						goto cleanup;
					}
					DeviceToken = 2;
				}
				else if (2 == DeviceToken)
				{
					if (1 != swscanf_s(szWorkingBuffer, L"%x", &(DeviceMap[NumDevices].Address[0])))
					{
						dwErr = ERROR_INVALID_DATA;
						goto cleanup;
					}
					DeviceToken = 3;
				}
				else if (3 == DeviceToken)
				{
					if (1 != swscanf_s(szWorkingBuffer, L"%x", &(DeviceMap[NumDevices].Address[1])))
                    {
						dwErr = ERROR_INVALID_DATA;
						goto cleanup;
					}
					DeviceToken = 4;
				}
				else if (4 == DeviceToken)
				{
					if (1 != swscanf_s(szWorkingBuffer, L"%x", &(DeviceMap[NumDevices].Address[2])))
					{
						dwErr = ERROR_INVALID_DATA;
						goto cleanup;
					}
					DeviceToken = 0;
					NumDevices++;
				}

				i += j;
				break;
			}
		}
	}

	if (0 == NumDevices)
	{
		dwErr = ERROR_NOT_FOUND;
		goto cleanup;
	}

	printf("Using port %S\n", szPortName);
	for (i=0; i < NumDevices; i++)
	{
		printf("Device %d = %d %d %d\n", DeviceMap[i].DeviceID, DeviceMap[i].Address[0], DeviceMap[i].Address[1], DeviceMap[i].Address[2]);
	}

	bRet = TRUE;
	dwErr = ERROR_SUCCESS;

cleanup:

	if (NULL != hConfigFile)
	{
		CloseHandle(hConfigFile);
	}

	if (NULL != szConfigFile)
	{
		HeapFree(GetProcessHeap(), 0, szConfigFile);
	}

	if (NULL != szWorkingBuffer)
	{
		HeapFree(GetProcessHeap(), 0, szWorkingBuffer);
	}

	SetLastError(dwErr);
	return bRet;
}

VOID
ControlDevice(
	IN int DeviceID,
	IN int Setting
	)
{
	HANDLE hPort = NULL;
	DWORD BytesTransferred = 0;
	DWORD BytesToSend = 8;
	BYTE SendBytes[8] = {2, 98, 0, 0, 0, 5, 17, 0};
	DWORD i = 0;

	printf("ControlDevice(%d, %d)\n", DeviceID, Setting);

	for (i=0; i < NumDevices; i++)
	{
		if (DeviceMap[i].DeviceID == DeviceID)
		{
			SendBytes[2] = DeviceMap[i].Address[0];
			SendBytes[3] = DeviceMap[i].Address[1];
			SendBytes[4] = DeviceMap[i].Address[2];
			break;
		}
	}

	if (i == NumDevices)
	{
		goto cleanup;
	}

	SendBytes[7] = ((255 * Setting) / 100);

	printf("Sending signal %x %x %x %x %x %x %x %x to port %S\n", 
		   SendBytes[0], 
		   SendBytes[1], 
		   SendBytes[2], 
		   SendBytes[3], 
		   SendBytes[4], 
		   SendBytes[5], 
		   SendBytes[6], 
		   SendBytes[7],
		   szPortName);

	hPort = CreateFile(szPortName, 
					   GENERIC_READ | GENERIC_WRITE, 
                       0, 
                       0, 
                       OPEN_EXISTING,
                       FILE_ATTRIBUTE_NORMAL,
                       0);

	if (NULL == hPort || INVALID_HANDLE_VALUE == hPort)
	{
		goto cleanup;
	}

	if (!WriteFile(hPort, SendBytes, BytesToSend, &BytesTransferred, NULL))
	{
		goto cleanup;
	}


cleanup:
	if (NULL != hPort && INVALID_HANDLE_VALUE != hPort)
	{
		CloseHandle(hPort);
	}

}
