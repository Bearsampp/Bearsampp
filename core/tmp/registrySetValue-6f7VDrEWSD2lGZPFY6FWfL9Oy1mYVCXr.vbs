On Error Resume Next
Dim XyfoJJPeSCoYuxn, ozWgdvlptbQfLgI, GuHpbKsVrBCLfJx, sNOsKJkPxtpKhQn
Set ozWgdvlptbQfLgI = CreateObject("scripting.filesystemobject")
Set GuHpbKsVrBCLfJx = ozWgdvlptbQfLgI.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-Y0X6IVmHZnLU4MPTYX4Co2fU8rBSGZVL.tmp", True)
Set sNOsKJkPxtpKhQn = ozWgdvlptbQfLgI.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-dpShYwAKB7OlvML0SATsAIlM63ugS9PA.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Const HKEY = &H80000002

Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue

newValue = ""
outFile = "E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-mlgRg7Qc0GMoKVeTK9x2bt70ZlvJv23o.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

objRegistry.DeleteValue HKEY, "SOFTWARE\Microsoft\Windows\CurrentVersion\Run", "Bearsampp"
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    objFile.Write "REG_NO_ERROR"
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
GuHpbKsVrBCLfJx.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
GuHpbKsVrBCLfJx.Write Err.Description
End If
sNOsKJkPxtpKhQn.Write "FINISHED!"
sNOsKJkPxtpKhQn.Close
GuHpbKsVrBCLfJx.Close
