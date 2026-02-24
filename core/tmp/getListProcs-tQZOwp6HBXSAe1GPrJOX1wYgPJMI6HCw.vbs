On Error Resume Next
Dim lhhevYNUYHUblzq, HzAJeZlqDkWmORm, vUvUQOJVdMBvhLM, cVtNmpNIogaqZHb
Set HzAJeZlqDkWmORm = CreateObject("scripting.filesystemobject")
Set vUvUQOJVdMBvhLM = HzAJeZlqDkWmORm.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-h1VMHIAvm2ugZQlZN8GpzNKSwzKKI1ZZ.tmp", True)
Set cVtNmpNIogaqZHb = HzAJeZlqDkWmORm.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-5845UiLH6mxHPeB8dFbOSxtBoz5i3sMP.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objFso, objResultFile, objWMIService

Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getListProcs-PhsIyHSrAcUOP6UrDbmsJNrq7iSGGcsb.vbs", True)
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
vUvUQOJVdMBvhLM.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
vUvUQOJVdMBvhLM.Write Err.Description
End If
cVtNmpNIogaqZHb.Write "FINISHED!"
cVtNmpNIogaqZHb.Close
vUvUQOJVdMBvhLM.Close
