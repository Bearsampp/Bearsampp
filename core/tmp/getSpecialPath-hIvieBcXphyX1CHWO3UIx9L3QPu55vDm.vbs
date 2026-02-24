On Error Resume Next
Dim sSfApXOAmlODZrE, dSHUOxIrIQxvhFC, kwSLqIxdqXPZHtG, oxsmuEXonnsHhmP
Set dSHUOxIrIQxvhFC = CreateObject("scripting.filesystemobject")
Set kwSLqIxdqXPZHtG = dSHUOxIrIQxvhFC.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-HYRFGdrvnUAHELsySW3fnqxt2wbb7FUO.tmp", True)
Set oxsmuEXonnsHhmP = dSHUOxIrIQxvhFC.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-4GQrBj3JRyT4x7fgU9fjGNn2dwFnqkGk.tmp", True)
startTime = Timer
timeoutSeconds = 10

Dim objShell, objFso, objResultFile

Set objShell = Wscript.CreateObject("Wscript.Shell")
Set objFso = CreateObject("scripting.filesystemobject")
Set objResultFile = objFso.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\getSpecialPath-98k7Xtt1kLc5lJvY73yOJZ8ye1sz7weA.vbs", True)

objResultFile.WriteLine(objShell.SpecialFolders("Startup"))
objResultFile.Close


If Timer - startTime > timeoutSeconds Then
kwSLqIxdqXPZHtG.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
kwSLqIxdqXPZHtG.Write Err.Description
End If
oxsmuEXonnsHhmP.Write "FINISHED!"
oxsmuEXonnsHhmP.Close
kwSLqIxdqXPZHtG.Close
