// stdafx.h : include file for standard system include files,
// or project specific include files that are used frequently, but
// are changed infrequently
//

#pragma once

#include "targetver.h"

#include <stdio.h>
#include <tchar.h>
#include <windows.h>
#include <winhttp.h>

extern void ControlDevice(int DeviceID, int Setting);
extern BOOL InitializeDevices(LPCTSTR lpConfigFile);

// TODO: reference additional headers your program requires here
