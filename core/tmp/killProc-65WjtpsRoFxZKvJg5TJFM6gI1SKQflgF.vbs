On Error Resume Next
Dim RdnlEwZgJyQBSHK, RLbnqLIRptfoUkK, KkHLOcFcwqmTaDF, vaNjsqblyIOwxRg
Set RLbnqLIRptfoUkK = CreateObject("scripting.filesystemobject")
Set KkHLOcFcwqmTaDF = RLbnqLIRptfoUkK.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-IAetXBSUH9N7geyR9UiiBRnouOtxRKul.tmp", True)
Set vaNjsqblyIOwxRg = RLbnqLIRptfoUkK.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-tNwUB0PlgScMLWDfW06v1rQ5W2IH8vbb.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService, processFound

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\killProc-9HlW7CXEb0nRrimJbgJdBsc35jPyitAd.vbs", True)
strComputer = "."
strProcessKill = "54456"
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
KkHLOcFcwqmTaDF.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
KkHLOcFcwqmTaDF.Write Err.Description
End If
vaNjsqblyIOwxRg.Write "FINISHED!"
vaNjsqblyIOwxRg.Close
KkHLOcFcwqmTaDF.Close
