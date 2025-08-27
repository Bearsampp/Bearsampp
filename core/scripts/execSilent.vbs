' Constants
Const ForAppending = 8
Const ForReading = 1
Const ForWriting = 2

' Variables for execution
Dim randomShell, randomFso, randomArgs

' Variables for logging
Dim scriptPath, logPath, logFile

' Initialize objects with error handling
On Error Resume Next
Set randomShell = WScript.CreateObject("WScript.Shell")
Set randomFso = CreateObject("Scripting.FileSystemObject")
Set randomArgs = WScript.Arguments

If Err.Number <> 0 Then
    WScript.Echo "Failed to initialize objects: " & Err.Description
    WScript.Quit 1
End If
On Error GoTo 0

' Setup logging with improved error handling
scriptPath = randomFso.GetParentFolderName(WScript.ScriptFullName)
logPath = randomFso.BuildPath(scriptPath, "..\logs\")

' Ensure logs directory exists
On Error Resume Next
If Not randomFso.FolderExists(logPath) Then
    randomFso.CreateFolder(logPath)
    If Err.Number <> 0 Then
    ' Silently continue if we can't create the directory
        Err.Clear
    End If
End If
On Error GoTo 0

logFile = randomFso.BuildPath(logPath, "bearsampp-vbs.log")

' Process arguments
num = randomArgs.Count
sargs = ""

If num = 0 Then
    SafeLogError "No arguments provided"
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

' Execute command with error handling
On Error Resume Next
Dim exitCode
exitCode = randomShell.Run("""" & randomArgs(0) & """" & sargs, 0, True)

If Err.Number <> 0 Then
    SafeLogError "Failed to execute: " & randomArgs(0) & " - " & Err.Description
    WScript.Quit 1
End If
On Error GoTo 0

SafeLogInfo "Successfully executed: " & randomArgs(0) & " with exit code: " & exitCode
WScript.Quit exitCode

' Improved logging functions with retry mechanism
Sub SafeLogError(message)
    SafeWriteToLog "ERROR", message
End Sub

Sub SafeLogInfo(message)
    SafeWriteToLog "INFO", message
End Sub

Sub SafeWriteToLog(level, message)
    On Error Resume Next
    Dim logStream, retryCount, maxRetries
    maxRetries = 3
    retryCount = 0

    Do While retryCount < maxRetries
        Err.Clear
        Set logStream = randomFso.OpenTextFile(logFile, ForAppending, True)

        If Err.Number = 0 Then
        ' Successfully opened file
            logStream.WriteLine Now & " [" & level & "] " & message
            logStream.Close
            Exit Sub
        Else
        ' Failed to open file, wait and retry
            retryCount = retryCount + 1
            If retryCount < maxRetries Then
                WScript.Sleep 50 ' Wait 50ms before retry
            End If
        End If
    Loop

    ' If all retries failed, silently continue without logging
    ' This prevents the permission denied popup
    Err.Clear
End Sub
