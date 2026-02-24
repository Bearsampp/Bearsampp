On Error Resume Next
Dim JHahpjFFLrulLhD, SybNeHIHXZboCXm, rlrueDXQpucXJID, lsihGsdvnGZdzAE
Set SybNeHIHXZboCXm = CreateObject("scripting.filesystemobject")
Set rlrueDXQpucXJID = SybNeHIHXZboCXm.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-YcjJ5RTxPMtZovrUrd0X2IkFv7KEkg94.tmp", True)
Set lsihGsdvnGZdzAE = SybNeHIHXZboCXm.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-DkoQVcDVqZ7L0w4VIhl3TIOZD6vVer3w.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-7a8tdfY8DzlL8jIT7D89lm68zjZKV8z8.vbs", True)
strComputer = "."
strProcessKill = "15724"
processFound = False
Set objWMIService = GetObject("winmgmts:" & "{impersonationLevel=impersonate}!\\" & strComputer & "\root\cimv2")
Set listProcess = objWMIService.ExecQuery ("Select * from Win32_Process Where ProcessID = " & strProcessKill)
For Each objProcess in listProcess
    processFound = True
    objResultFile.WriteLine(objProcess.Name & " || " & objProcess.ProcessID & " || " & objProcess.ExecutablePath)
    objProcess.Terminate()
Next
If Not processFound Then
    objResultFile.WriteLine("PROCESS_NOT_FOUND || " & strProcessKill & " || ")
End If
objResultFile.Close


If Timer - startTime > timeoutSeconds Then
rlrueDXQpucXJID.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
rlrueDXQpucXJID.Write Err.Description
End If
lsihGsdvnGZdzAE.Write "FINISHED!"
lsihGsdvnGZdzAE.Close
rlrueDXQpucXJID.Close
