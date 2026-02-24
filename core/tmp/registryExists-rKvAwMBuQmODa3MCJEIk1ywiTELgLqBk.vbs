On Error Resume Next
Dim ZlkUVrGYlZKSPrb, PQdcgArGHKUFDas, MWIbUVKEaLWZLnV, lELMygvCvUoWjgD
Set PQdcgArGHKUFDas = CreateObject("scripting.filesystemobject")
Set MWIbUVKEaLWZLnV = PQdcgArGHKUFDas.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-bnOIOtBYi1NHypG0hWoVAaqv47C0Gy6K.tmp", True)
Set lELMygvCvUoWjgD = PQdcgArGHKUFDas.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryExists-1ig8W2FUpxXAcDAWWgUhq8Rva48ZbYQr.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, bExists

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryExists-LHg0w8Kha139oIcTDXcNcNEs4ldXkIpO.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

strKey = "HKLM\SYSTEM\CurrentControlSet\Services\bearsamppxlight\Parameters\AppParameters"
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
MWIbUVKEaLWZLnV.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
MWIbUVKEaLWZLnV.Write Err.Description
End If
lELMygvCvUoWjgD.Write "FINISHED!"
lELMygvCvUoWjgD.Close
MWIbUVKEaLWZLnV.Close
