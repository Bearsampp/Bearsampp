On Error Resume Next
Dim GNUogOBXPlvzelq, wixNKBClTQXdjPV, WLexDRODzQNtqJt, THHpBVgsAbxkFBN
Set wixNKBClTQXdjPV = CreateObject("scripting.filesystemobject")
Set WLexDRODzQNtqJt = wixNKBClTQXdjPV.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-uyjuicI9rtWmwRUKNO2wfTPxzdFaSwXV.tmp", True)
Set THHpBVgsAbxkFBN = wixNKBClTQXdjPV.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-NYrcbHkY4S79m32c2znLD8wWCA6KCAZA.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getServiceInfos-n9k1zTMidWif7QRp1tHCCQ6CzbPqTtrC.vbs", True)
strComputer = "."
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listServices = objWMIService.ExecQuery ("SELECT * FROM Win32_Service WHERE Name='bearsamppmariadb'")
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
WLexDRODzQNtqJt.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
WLexDRODzQNtqJt.Write Err.Description
End If
THHpBVgsAbxkFBN.Write "FINISHED!"
THHpBVgsAbxkFBN.Close
WLexDRODzQNtqJt.Close
