On Error Resume Next
Dim TyvPiCHPfJhoLmt, maGCAvmNTQUxutl, ONRnGgxlfoOHnMK, ybEAfxwfKxcGnQK
Set maGCAvmNTQUxutl = CreateObject("scripting.filesystemobject")
Set ONRnGgxlfoOHnMK = maGCAvmNTQUxutl.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-4KGgES4ovvF7VrnQ53oO0qs0N6n6xsK2.tmp", True)
Set ybEAfxwfKxcGnQK = maGCAvmNTQUxutl.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-yCzYjoeSuXqIsXCEvJfj9rwdlo0kx96N.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, bExists

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryExists-lRoJZadHwIZKk7K8noitDIfjOxErN0Wt.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

strKey = "HKLM\SYSTEM\CurrentControlSet\Services\bearsamppmailpit\Parameters\AppParameters"
entryValue = objShell.RegRead(strKey)
If Err.Number <> 0 Then
    If Right(strKey,1) = "\" Then
        If Instr(1, Err.Description, ssig, 1) <> 0 Then
            bExists = true
        Else
            bExists = false
        End If
    Else
        bExists = false
    End If
    Err.Clear
Else
    bExists = true
End If

On Error Goto 0
If bExists = vbFalse Then
    objFile.Write "0"
Else
    objFile.Write "1"
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
ONRnGgxlfoOHnMK.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
ONRnGgxlfoOHnMK.Write Err.Description
End If
ybEAfxwfKxcGnQK.Write "FINISHED!"
ybEAfxwfKxcGnQK.Close
ONRnGgxlfoOHnMK.Close
