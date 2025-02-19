@echo off
REM Batch file to set the icon for bearsampp.exe using rcedit

REM Define the path to the icon
SET ICON_PATH=core\resources\homepage\img\icons\bearsampp.ico

REM Define the path to the executable
SET EXECUTABLE_PATH=base\bearsampp.exe

REM Check if rcedit-x64.exe is in the current directory or specify the full path
SET RESOURCE_HACKER_PATH=ResourceHacker.exe

REM Execute rcedit to set the icon
%RESOURCE_HACKER_PATH% -open "%EXECUTABLE_PATH%" -save "%EXECUTABLE_PATH%" -action modify -res "%ICON_PATH%", -mask ICONGROUP, MAINICON,

REM Check if the command was successful
IF %ERRORLEVEL% EQU 0 (
    echo Icon set successfully.
) ELSE (
    echo Failed to set icon. Please check the paths and try again.
)