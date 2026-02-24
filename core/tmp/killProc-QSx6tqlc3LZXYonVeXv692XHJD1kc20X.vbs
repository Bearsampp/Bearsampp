On Error Resume Next
Dim mQDiDwsANtILMvS, UyIqivcMfmfMCzH, pIfAspTYjUhSXfV, ZkwEhoATeeScBIP
Set UyIqivcMfmfMCzH = CreateObject("scripting.filesystemobject")
Set pIfAspTYjUhSXfV = UyIqivcMfmfMCzH.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-3zxEs0ol3RuBJtUMGrAjan0Qo8mOjAgY.tmp", True)
Set ZkwEhoATeeScBIP = UyIqivcMfmfMCzH.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-9ihdW5ZLJiMuV7nJSPJj1Td5dxiG4Bqn.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-ZMjS2FnuWGObV4uhK0MgIQj4Y9zaC3HZ.vbs", True)
strComputer = "."
strProcessKill = "53604"
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
pIfAspTYjUhSXfV.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
pIfAspTYjUhSXfV.Write Err.Description
End If
ZkwEhoATeeScBIP.Write "FINISHED!"
ZkwEhoATeeScBIP.Close
pIfAspTYjUhSXfV.Close
