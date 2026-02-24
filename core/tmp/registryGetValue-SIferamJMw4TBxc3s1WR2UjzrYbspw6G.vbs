On Error Resume Next
Dim vsEonxXKofgrhHm, RaEgBVIkciXsIJu, pYPvryebWbjbRoa, ILMTrToTMSogFnm
Set RaEgBVIkciXsIJu = CreateObject("scripting.filesystemobject")
Set pYPvryebWbjbRoa = RaEgBVIkciXsIJu.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-hIqitDIWiegM8ohw7rzRnVIRHnzOmKR4.tmp", True)
Set ILMTrToTMSogFnm = RaEgBVIkciXsIJu.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-6KBj8T2Sbi7d9lmT8Z0404AfK1s2QXeL.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, entryValue

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-hd8Q5PZdUNmVePLXHIXzEmmxFQ6Ql1T7.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment\BEARSAMPP_PATH")
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    objFile.Write entryValue
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
pYPvryebWbjbRoa.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
pYPvryebWbjbRoa.Write Err.Description
End If
ILMTrToTMSogFnm.Write "FINISHED!"
ILMTrToTMSogFnm.Close
pYPvryebWbjbRoa.Close
