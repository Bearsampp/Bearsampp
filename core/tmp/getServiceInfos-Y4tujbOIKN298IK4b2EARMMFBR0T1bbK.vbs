On Error Resume Next
Dim djtpRJCLzLSkPxX, PKSFhJqVRCXPVdV, LaeBIFfyFHSwoWU, ujfQNOQPGfCUJBq
Set PKSFhJqVRCXPVdV = CreateObject("scripting.filesystemobject")
Set LaeBIFfyFHSwoWU = PKSFhJqVRCXPVdV.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-k6tXp9IAt7868anaUCpcYwCO4v1fSlrN.tmp", True)
Set ujfQNOQPGfCUJBq = PKSFhJqVRCXPVdV.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-3l8JZK06h1nZhiBcdxSDtp9RBbBqmy65.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-RLO9VVyQI55XeKak173m7C7bHEixaMKl.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name='bearsamppmailpit'")
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
LaeBIFfyFHSwoWU.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
LaeBIFfyFHSwoWU.Write Err.Description
End If
ujfQNOQPGfCUJBq.Write "FINISHED!"
ujfQNOQPGfCUJBq.Close
LaeBIFfyFHSwoWU.Close
