On Error Resume Next
Err.Clear

' Constants
Const ForAppending = 8

' Variables for execution
Dim randomShell, randomFso, randomArgs

' Variables for logging
Dim scriptPath, logPath, logFile

' Initialize objects
Set randomShell = WScript.CreateObject("WScript.Shell")
Set randomFso = CreateObject("Scripting.FileSystemObject")
Set randomArgs = WScript.Arguments

' Setup logging
scriptPath = randomFso.GetParentFolderName(WScript.ScriptFullName)
logPath = randomFso.BuildPath(scriptPath, "..\logs\")

If Not randomFso.FolderExists(logPath) Then
    randomFso.CreateFolder(logPath)
End If

logFile = randomFso.BuildPath(logPath, "bearsampp-vbs.log")

' Check for initialization errors
If Err.Number <> 0 Then
    WScript.Echo "Error: " & Err.Description
    WScript.Quit 1
End If

num = randomArgs.Count
sargs = ""

If num = 0 Then
    LogError "No arguments provided"
    WScript.Quit 1
End If

If num > 1 Then
    Dim argArray()
    ReDim argArray(num - 1)
    For k = 1 To num - 1
        argArray(k - 1) = randomArgs.Item(k)
    Next
    sargs = " " & Join(argArray, " ") & " "
End If

Dim exitCode
exitCode = randomShell.Run("""" & randomArgs(0) & """" & sargs, 0, True)

If Err.Number <> 0 Then
    LogError "Failed to execute: " & randomArgs(0) & " - " & Err.Description
    WScript.Quit 1
End If

LogInfo "Successfully executed: " & randomArgs(0) & " with exit code: " & exitCode
WScript.Quit exitCode

' Logging functions
Sub LogError(message)
    WriteToLog "ERROR", message
End Sub

Sub LogInfo(message)
    WriteToLog "INFO", message
End Sub

Sub WriteToLog(level, message)
    On Error Resume Next
    Dim logStream
    Set logStream = randomFso.OpenTextFile(logFile, ForAppending, True)
    If Err.Number <> 0 Then
        WScript.Echo "Log Error: " & Err.Description
        Err.Clear
        WScript.Quit 1
    End If
    logStream.WriteLine Now & " [" & level & "] " & message
    logStream.Close
End Sub
