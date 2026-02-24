On Error Resume Next
Dim pbKSSFEwQbiSwCL, jcaAyqibNyHNLvx, MEVHYhqjzHYfATq, ZuhUTQiAQwgkwDZ
Set jcaAyqibNyHNLvx = CreateObject("scripting.filesystemobject")
Set MEVHYhqjzHYfATq = jcaAyqibNyHNLvx.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-r6dY3ievH1Ruc9BRuPyZrHD3jyExH5TC.tmp", True)
Set ZuhUTQiAQwgkwDZ = jcaAyqibNyHNLvx.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-54yLsDjxHIHjimiKb2bOc8kYStTAvRCt.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objShell, objFso, objResultFile

Set objShell = Wscript.CreateObject("Wscript.Shell")
Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-iCwXrxp0uJ2rKx1weuUCtB3OyVpfb8xk.vbs", True)

objResultFile.WriteLine(objShell.SpecialFolders("Startup"))
objResultFile.Close


If Timer - startTime > timeoutSeconds Then
MEVHYhqjzHYfATq.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
MEVHYhqjzHYfATq.Write Err.Description
End If
ZuhUTQiAQwgkwDZ.Write "FINISHED!"
ZuhUTQiAQwgkwDZ.Close
MEVHYhqjzHYfATq.Close
