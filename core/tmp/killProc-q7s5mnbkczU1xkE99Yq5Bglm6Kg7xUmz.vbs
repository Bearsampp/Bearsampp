On Error Resume Next
Dim boOdhclTZGlQBVj, cqNzifYpQMGLULL, CdzCSmOpxKRnDiN, odFIjdinTebMqIl
Set cqNzifYpQMGLULL = CreateObject("scripting.filesystemobject")
Set CdzCSmOpxKRnDiN = cqNzifYpQMGLULL.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-Et1A1wjR6bp95XrckXhtvERi9hEH39bu.tmp", True)
Set odFIjdinTebMqIl = cqNzifYpQMGLULL.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-0UgAneOK7avVCc54ltFElNVzmoRqtsxF.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-i1Gt5e8eb4eO2e4eE998V4U7P3dDEWRV.vbs", True)
strComputer = "."
strProcessKill = "49664"
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
CdzCSmOpxKRnDiN.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
CdzCSmOpxKRnDiN.Write Err.Description
End If
odFIjdinTebMqIl.Write "FINISHED!"
odFIjdinTebMqIl.Close
CdzCSmOpxKRnDiN.Close
