On Error Resume Next
Dim RwLzmzrKpHHGmUz, zeuCjIKgSyFdNWO, nRbctbSIVQrHslg, LdrRToWpxWhzYSi
Set zeuCjIKgSyFdNWO = CreateObject("scripting.filesystemobject")
Set nRbctbSIVQrHslg = zeuCjIKgSyFdNWO.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-XYSdmg6Dd6IuIMz1N9zBw50nlEW8iXiO.tmp", True)
Set LdrRToWpxWhzYSi = zeuCjIKgSyFdNWO.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-hzpjqV7OAe3r4WCAUYVVoGp5qoSpVjhW.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-cZQi9WI9xrd0Wf3q27QroTAS0qlVOtV1.vbs", True)
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
nRbctbSIVQrHslg.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
nRbctbSIVQrHslg.Write Err.Description
End If
LdrRToWpxWhzYSi.Write "FINISHED!"
LdrRToWpxWhzYSi.Close
nRbctbSIVQrHslg.Close
