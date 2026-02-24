On Error Resume Next
Dim LefOGVlQzElWCgG, nqIwOoBZMVmVkYD, JvANyGzDDLoCREG, ePRTjLDCtSvZSBu
Set nqIwOoBZMVmVkYD = CreateObject("scripting.filesystemobject")
Set JvANyGzDDLoCREG = nqIwOoBZMVmVkYD.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-RVKfcIOxobJFukMHlKHoV7JDB9by11Si.tmp", True)
Set ePRTjLDCtSvZSBu = nqIwOoBZMVmVkYD.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-1OsfwLRpiOwQX0JZodH8BPkX55dYVOEf.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objShell, objFso, objResultFile

Set objShell = Wscript.CreateObject("Wscript.Shell")
Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-h3yuROrTMioYDd5WBRQKaddkDZDtYegu.vbs", True)

objResultFile.WriteLine(objShell.SpecialFolders("Startup"))
objResultFile.Close


If Timer - startTime > timeoutSeconds Then
JvANyGzDDLoCREG.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
JvANyGzDDLoCREG.Write Err.Description
End If
ePRTjLDCtSvZSBu.Write "FINISHED!"
ePRTjLDCtSvZSBu.Close
JvANyGzDDLoCREG.Close
