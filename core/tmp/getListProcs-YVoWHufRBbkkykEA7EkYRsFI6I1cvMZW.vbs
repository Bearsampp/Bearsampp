On Error Resume Next
Dim gqAAjiVKOxQefwZ, azmWwCeHHIASave, vWLmLuVWQOdnTPp, mUdMBppNkHGJkTK
Set azmWwCeHHIASave = CreateObject("scripting.filesystemobject")
Set vWLmLuVWQOdnTPp = azmWwCeHHIASave.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-A3Mt0SH49nnYnJqjxBJpGe4rsVpHrzKX.tmp", True)
Set mUdMBppNkHGJkTK = azmWwCeHHIASave.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-EKkvXvKT8gVQfmTnTkVsg7a8vyFPvwS5.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-ijeVhbjBc1c4PsEfQKerGvL79hL2oswJ.vbs", True)
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
vWLmLuVWQOdnTPp.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
vWLmLuVWQOdnTPp.Write Err.Description
End If
mUdMBppNkHGJkTK.Write "FINISHED!"
mUdMBppNkHGJkTK.Close
vWLmLuVWQOdnTPp.Close
