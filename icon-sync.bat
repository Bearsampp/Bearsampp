@echo off

REM Copy bearsampp executable
copy "%~dp0bin\tmp\release\base\bearsampp.exe" "%~dp0..\sandbox\bearsampp.exe" /Y

REM Copy version.dat
copy "%~dp0bin\tmp\release\base\core\resources\version.dat" "%~dp0..\sandbox\core\resources\version.dat" /Y

REM Run rcedit to set icon
"%~dp0rcedit-x64.exe" "%~dp0..\sandbox\bearsampp.exe" --set-icon "..\sandbox\core\resources\homepage\img\icons\bearsampp.ico"

echo Process completed.
