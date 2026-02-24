On Error Resume Next
Dim qnVdXoSkwLbnMDE, AXiDhKOLzqsBiYv, GXGWmwtXkadZdbC, GodFaObjZVLPvtY
Set AXiDhKOLzqsBiYv = CreateObject("scripting.filesystemobject")
Set GXGWmwtXkadZdbC = AXiDhKOLzqsBiYv.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-bxYjONHdIsE0bI9nk3AdwDva7JaTU9yW.tmp", True)
Set GodFaObjZVLPvtY = AXiDhKOLzqsBiYv.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-LhstCKxxObprUxP5383bH3XRYu8RAGWw.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-iU8CWaeK35DKQYxWQTdp5UBS3QGJmicp.vbs", True)
strComputer = "."
strProcessKill = "31932"
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
GXGWmwtXkadZdbC.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
GXGWmwtXkadZdbC.Write Err.Description
End If
GodFaObjZVLPvtY.Write "FINISHED!"
GodFaObjZVLPvtY.Close
GXGWmwtXkadZdbC.Close
