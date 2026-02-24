On Error Resume Next
Dim JXngOMQddPfCRUp, fSDHYriIPAiNUBf, CWBhStdsdjdsRpP, jyJOwmAzlaMIBEz
Set fSDHYriIPAiNUBf = CreateObject("scripting.filesystemobject")
Set CWBhStdsdjdsRpP = fSDHYriIPAiNUBf.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-l2OiJwfvgKPVNk2xYO6jiVfJk73U8H3C.tmp", True)
Set jyJOwmAzlaMIBEz = fSDHYriIPAiNUBf.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-WkXZROu8CFa1h4Ni6PKMpT4ykahv4GbL.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Const HKEY = &H80000002

Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue

newValue = "-m 512 -p 11211 -U 0 -vv"
outFile = "E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-PlnObPUb57scy3rlkpJW7d5DlRGSitY4.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

objRegistry.SetExpandedStringValue HKEY, "SYSTEM\CurrentControlSet\Services\bearsamppmemcached\Parameters", "AppParameters", newValue
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Services\bearsamppmemcached\Parameters\AppParameters")
    If entryValue = newValue Then
        objFile.Write "REG_NO_ERROR"
    Else
        objFile.Write "REG_ERROR_SET" & newValue
    End If
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
CWBhStdsdjdsRpP.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
CWBhStdsdjdsRpP.Write Err.Description
End If
jyJOwmAzlaMIBEz.Write "FINISHED!"
jyJOwmAzlaMIBEz.Close
CWBhStdsdjdsRpP.Close
