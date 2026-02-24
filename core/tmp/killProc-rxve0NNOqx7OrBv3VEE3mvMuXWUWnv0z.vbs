On Error Resume Next
Dim MaVCTtKMUrTjeMG, NjdvqzGyQNuupkG, tWMJreejZogIiWI, hQjCaEJxcPUgzMw
Set NjdvqzGyQNuupkG = CreateObject("scripting.filesystemobject")
Set tWMJreejZogIiWI = NjdvqzGyQNuupkG.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-1j3VDRoFMnqiIDwiU3PeQqD69gE31iqY.tmp", True)
Set hQjCaEJxcPUgzMw = NjdvqzGyQNuupkG.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-gsbURvkrBVtDrkIwZqw7KhoJurn5x7bR.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-JbLwku9iL0ajOh5iZJnPKjHTbBJF9zrG.vbs", True)
strComputer = "."
strProcessKill = "46316"
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
tWMJreejZogIiWI.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
tWMJreejZogIiWI.Write Err.Description
End If
hQjCaEJxcPUgzMw.Write "FINISHED!"
hQjCaEJxcPUgzMw.Close
tWMJreejZogIiWI.Close
