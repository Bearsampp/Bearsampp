On Error Resume Next
Dim vGDTNofwhEDnlMQ, eOlJnmPGeBXTJOY, rMKPSRkdiNPDDrK, ItaGCHZqnmwiJyH
Set eOlJnmPGeBXTJOY = CreateObject("scripting.filesystemobject")
Set rMKPSRkdiNPDDrK = eOlJnmPGeBXTJOY.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-KNsxyL3hazjlKPgr6qKUQCPAU7yVxACV.tmp", True)
Set ItaGCHZqnmwiJyH = eOlJnmPGeBXTJOY.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-H9SO9KhdRWKLqJzel5Blu0wiTLYM4l9t.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-mSxltkSN2x9YDdjtIwET33n278wLcXBJ.vbs", True)
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
rMKPSRkdiNPDDrK.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
rMKPSRkdiNPDDrK.Write Err.Description
End If
ItaGCHZqnmwiJyH.Write "FINISHED!"
ItaGCHZqnmwiJyH.Close
rMKPSRkdiNPDDrK.Close
