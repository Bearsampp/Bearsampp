On Error Resume Next
Dim nPzHOphGSqcqlsK, MHaCFCBtrjQLlPc, AJqUZTmECUMnaVN, hIFxHdJoruAxvZb
Set MHaCFCBtrjQLlPc = CreateObject("scripting.filesystemobject")
Set AJqUZTmECUMnaVN = MHaCFCBtrjQLlPc.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-m4GlvNsRVJUqvPqzZ30V2YnzE77vshP0.tmp", True)
Set hIFxHdJoruAxvZb = MHaCFCBtrjQLlPc.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-x5vyW2o1cOJ4rmW4nnYZEb0ojNWDiJOH.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-iBBrxSC0ElUKo9x0OY8w7nP4Fe7ditQC.vbs", True)
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
AJqUZTmECUMnaVN.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
AJqUZTmECUMnaVN.Write Err.Description
End If
hIFxHdJoruAxvZb.Write "FINISHED!"
hIFxHdJoruAxvZb.Close
AJqUZTmECUMnaVN.Close
