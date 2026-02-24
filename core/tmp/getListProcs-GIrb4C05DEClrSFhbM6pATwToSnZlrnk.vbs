On Error Resume Next
Dim eUBpqiQsHjKJXMK, wTmkcssOPXMPcUK, bkRuEdUhlNyjWHT, PKVTzulZOSZVxfS
Set wTmkcssOPXMPcUK = CreateObject("scripting.filesystemobject")
Set bkRuEdUhlNyjWHT = wTmkcssOPXMPcUK.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-ZI0kLJJsVQfUBesLeUBcPneEIqYcsqQi.tmp", True)
Set PKVTzulZOSZVxfS = wTmkcssOPXMPcUK.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-TuWbFQWMi8Yw5xhGYqrEZt3fL2or0G47.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-ngcghYFywbvT1wedu65cNYx6bPyzSI85.vbs", True)
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
bkRuEdUhlNyjWHT.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
bkRuEdUhlNyjWHT.Write Err.Description
End If
PKVTzulZOSZVxfS.Write "FINISHED!"
PKVTzulZOSZVxfS.Close
bkRuEdUhlNyjWHT.Close
