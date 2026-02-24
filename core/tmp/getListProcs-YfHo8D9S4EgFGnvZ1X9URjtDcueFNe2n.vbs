On Error Resume Next
Dim VAtKKeAbtTVregg, RaVIPsupXDyBYXc, sBKOApOtWkKGXqp, SktUMdXLlDvRsqE
Set RaVIPsupXDyBYXc = CreateObject("scripting.filesystemobject")
Set sBKOApOtWkKGXqp = RaVIPsupXDyBYXc.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-Ey7ZPHkrc7PY9bQmaxuJLd4LfN06f6kL.tmp", True)
Set SktUMdXLlDvRsqE = RaVIPsupXDyBYXc.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-CStg1J9YLeL1Z9ouI8xxBCedYHNsRS76.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-Jg5IZgNdrdNsaGy9rMPXVWi83Apa9TK4.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listProcess = objWMIService.ExecQuery ("SELECT * FROM Win32_Process")
For Each process in listProcess
    objResultFile.WriteLine(_
        process.Name & " || " & _
        process.ProcessID & " || " & _
        process.ExecutablePath & " || " & _
        process.Caption & " || " & _
        process.CommandLine )
Next
objResultFile.WriteLine("FINISHED!")
objResultFile.Close
Err.Clear


If Timer - startTime > timeoutSeconds Then
sBKOApOtWkKGXqp.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
sBKOApOtWkKGXqp.Write Err.Description
End If
SktUMdXLlDvRsqE.Write "FINISHED!"
SktUMdXLlDvRsqE.Close
sBKOApOtWkKGXqp.Close
