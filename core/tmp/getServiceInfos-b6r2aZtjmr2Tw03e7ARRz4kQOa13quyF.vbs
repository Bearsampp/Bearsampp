On Error Resume Next
Dim kJcHyflQPhaZmWj, IBBTDpKgTzqmIqH, AsgrjDorvrfRxIb, TZARfbiDPmjjwgV
Set IBBTDpKgTzqmIqH = CreateObject("scripting.filesystemobject")
Set AsgrjDorvrfRxIb = IBBTDpKgTzqmIqH.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-rKc1966LX9Ozm2DjNcGJneMK8vGYSOXZ.tmp", True)
Set TZARfbiDPmjjwgV = IBBTDpKgTzqmIqH.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-0jdi7odrRan5j5vUINtXRtxjjdTzKULU.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-Z5yADu1SqMPEq3BoNW0p2LOcMy1imANo.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name='bearsamppxlight'")
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
AsgrjDorvrfRxIb.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
AsgrjDorvrfRxIb.Write Err.Description
End If
TZARfbiDPmjjwgV.Write "FINISHED!"
TZARfbiDPmjjwgV.Close
AsgrjDorvrfRxIb.Close
