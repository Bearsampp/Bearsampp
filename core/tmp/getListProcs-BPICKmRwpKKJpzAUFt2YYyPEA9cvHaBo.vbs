On Error Resume Next
Dim qAfkXKEuDceHLWI, UGqMCBQuXZWQXrT, GijBIVZrZRCGMxz, qlKpKRPHDRcwJYe
Set UGqMCBQuXZWQXrT = CreateObject("scripting.filesystemobject")
Set GijBIVZrZRCGMxz = UGqMCBQuXZWQXrT.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-8Pcgz07fKnoh4zfmfOVhj0AHfwFTy7fW.tmp", True)
Set qlKpKRPHDRcwJYe = UGqMCBQuXZWQXrT.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-ZMYv1HSOVSWDCH3Jl8AveAGuIkzUh6f6.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-VX8vb0f35sz4B1L2W3wBGHRt8tpj57I4.vbs", True)
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
GijBIVZrZRCGMxz.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
GijBIVZrZRCGMxz.Write Err.Description
End If
qlKpKRPHDRcwJYe.Write "FINISHED!"
qlKpKRPHDRcwJYe.Close
GijBIVZrZRCGMxz.Close
