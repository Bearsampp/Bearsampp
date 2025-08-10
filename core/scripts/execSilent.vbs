On Error Resume Next
Err.Clear

' Constants
Const ForAppending = 8

' Input validation constants
Const MAX_ARGS = 50
Const MAX_ARG_LENGTH = 1000
Const MAX_COMMAND_LENGTH = 2000

' Blocked patterns for input validation
Dim blockedPatterns
blockedPatterns = Array( _
    "..", "\\", "/", "|", "&", ";", "`", "$", _
    "eval", "exec", "system", "shell", "cmd", _
    "<", ">", "net ", "reg ", "sc ", "taskkill" _
)

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

' Input validation - check argument count
If num = 0 Then
    LogError "No arguments provided"
    WScript.Quit 1
End If

If num > MAX_ARGS Then
    LogError "Too many arguments provided: " & num & " exceeds maximum: " & MAX_ARGS
    WScript.Quit 1
End If

' Validate command length
Dim commandPath
commandPath = Trim(randomArgs(0))
If Len(commandPath) > MAX_COMMAND_LENGTH Then
    LogError "Command path too long: " & Len(commandPath) & " exceeds maximum: " & MAX_COMMAND_LENGTH
    WScript.Quit 1
End If

' Validate and sanitize arguments
If num > 1 Then
    Dim argArray()
    ReDim argArray(num - 1)
    For k = 1 To num - 1
        Dim currentArg
        currentArg = randomArgs.Item(k)

        ' Validate each argument
        If Not ValidateArgument(currentArg) Then
            LogError "Invalid argument detected at position " & k
            WScript.Quit 1
        End If

        ' Sanitize the argument
        argArray(k - 1) = SanitizeArgument(currentArg)
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

' Input validation functions
Function ValidateArgument(arg)
    ValidateArgument = True

    ' Length validation
    If Len(arg) > MAX_ARG_LENGTH Then
        ValidateArgument = False
        Exit Function
    End If

    ' Pattern-based blocking
    Dim i, pattern
    For i = 0 To UBound(blockedPatterns)
        pattern = blockedPatterns(i)
        If InStr(LCase(arg), LCase(pattern)) > 0 Then
            ValidateArgument = False
            Exit Function
        End If
    Next

    ' Check for null bytes and control characters
    Dim j
    For j = 1 To Len(arg)
        Dim charCode
        charCode = Asc(Mid(arg, j, 1))
        If charCode = 0 Or (charCode >= 1 And charCode <= 31 And charCode <> 9 And charCode <> 10 And charCode <> 13) Then
            ValidateArgument = False
            Exit Function
        End If
    Next
End Function

Function SanitizeArgument(arg)
    Dim result
    result = arg

    ' Remove null bytes and dangerous control characters
    result = Replace(result, Chr(0), "")

    ' Remove other control characters except tab, LF, CR
    Dim i
    For i = 1 To 31
        If i <> 9 And i <> 10 And i <> 13 Then
            result = Replace(result, Chr(i), "")
        End If
    Next

    ' Escape quotes properly
    result = Replace(result, """", """""")

    SanitizeArgument = result
End Function

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
