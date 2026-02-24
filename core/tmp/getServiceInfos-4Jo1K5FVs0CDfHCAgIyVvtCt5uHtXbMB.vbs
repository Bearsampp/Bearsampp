On Error Resume Next
Dim KmoqqaJvqbWblal, YHqzAILGukyQQDJ, IXlYIoltVSjWLYv, vqCYCNiLRqhsIqr
Set YHqzAILGukyQQDJ = CreateObject("scripting.filesystemobject")
Set IXlYIoltVSjWLYv = YHqzAILGukyQQDJ.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-jKIaU6tQ4SV2WMMvSC3QsKnE8R65zERb.tmp", True)
Set vqCYCNiLRqhsIqr = YHqzAILGukyQQDJ.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-URrZcFJhtwXPVOX173poYrCpkOlKRHmd.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-O5FsDdLgygHxMwlIZJFhn85qgCv9nKok.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name='bearsamppmemcached'")
For Each service in listServices
    objResultFile.WriteLine(_
        service.Name & " || " & _
        service.DisplayName & " || " & _
        service.Description & " || " & _
        service.PathName & " || " & _
        service.State )
Next
objResultFile.WriteLine("FINISHED!")
objResultFile.Close


If Timer - startTime > timeoutSeconds Then
IXlYIoltVSjWLYv.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
IXlYIoltVSjWLYv.Write Err.Description
End If
vqCYCNiLRqhsIqr.Write "FINISHED!"
vqCYCNiLRqhsIqr.Close
IXlYIoltVSjWLYv.Close
