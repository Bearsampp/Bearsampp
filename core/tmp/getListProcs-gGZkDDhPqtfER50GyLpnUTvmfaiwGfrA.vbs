On Error Resume Next
Dim OAyDyUMbYWcqXli, sRjVHIRHDocBNDw, uCiXSEemVbzArox, wMHjZGMsWQTAYcn
Set sRjVHIRHDocBNDw = CreateObject("scripting.filesystemobject")
Set uCiXSEemVbzArox = sRjVHIRHDocBNDw.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-DH17fMvF6YjWh1a9ORuo6mjmPamJroyH.tmp", True)
Set wMHjZGMsWQTAYcn = sRjVHIRHDocBNDw.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-ACGRVsItv5J1hhNbENGO8D9IUHUaetUH.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-p9KgWIdcSXfgbvxYyiMpYPeM66tx2ryF.vbs", True)
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
uCiXSEemVbzArox.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
uCiXSEemVbzArox.Write Err.Description
End If
wMHjZGMsWQTAYcn.Write "FINISHED!"
wMHjZGMsWQTAYcn.Close
uCiXSEemVbzArox.Close
