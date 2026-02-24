On Error Resume Next
Dim scqMpHDljWIfxqV, uFAkKrNBlGESwRE, IiLpRkkVlxlwRuK, rhwHiBqNrJaJqdi
Set uFAkKrNBlGESwRE = CreateObject("scripting.filesystemobject")
Set IiLpRkkVlxlwRuK = uFAkKrNBlGESwRE.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-tffw6ztLoei8IJ1FG5Dt7At84bWmVOdE.tmp", True)
Set rhwHiBqNrJaJqdi = uFAkKrNBlGESwRE.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-NijtvbrugXAQ5PRo36oH7I1zaSCCNqRT.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, bExists

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryExists-VNYtAzzFkxV5nn3CqmdEWi6JLpFyTYUW.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

strKey = "HKLM\SYSTEM\CurrentControlSet\Services\bearsamppmemcached\Parameters\AppParameters"
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
IiLpRkkVlxlwRuK.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
IiLpRkkVlxlwRuK.Write Err.Description
End If
rhwHiBqNrJaJqdi.Write "FINISHED!"
rhwHiBqNrJaJqdi.Close
IiLpRkkVlxlwRuK.Close
