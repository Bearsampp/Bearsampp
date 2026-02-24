On Error Resume Next
Dim OlOJjyJmRQRcrSz, egFZVtpiXGVFhvo, OZzcpYCIzIccuPR, EcXffsTDFjuSyDd
Set egFZVtpiXGVFhvo = CreateObject("scripting.filesystemobject")
Set OZzcpYCIzIccuPR = egFZVtpiXGVFhvo.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-LQbiQC3he6zTcNVB81fjOJaNiVCOVXRV.tmp", True)
Set EcXffsTDFjuSyDd = egFZVtpiXGVFhvo.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-FYAeI8n6fo9KV1RtjAu0AVdRJfHn7cgS.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-ngMgfomPwyVup5Lh5PmTOqFNcYOS402T.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name='bearsampppostgresql'")
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
OZzcpYCIzIccuPR.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
OZzcpYCIzIccuPR.Write Err.Description
End If
EcXffsTDFjuSyDd.Write "FINISHED!"
EcXffsTDFjuSyDd.Close
OZzcpYCIzIccuPR.Close
