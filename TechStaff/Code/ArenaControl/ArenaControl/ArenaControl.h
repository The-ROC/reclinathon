#include <windows.h>

typedef struct _DEVICE_ADDRESS
{
	int DeviceID;
	BYTE Address[3];
} DEVICE_ADDRESS, *PDEVICE_ADDRESS;

