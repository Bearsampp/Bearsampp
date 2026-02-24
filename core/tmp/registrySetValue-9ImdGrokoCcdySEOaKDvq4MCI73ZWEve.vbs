On Error Resume Next
Dim KdiSpWyhPYyOovA, cpOZYZZPXLGMDYz, FrSOMeoATRynaIe, UiSVfZeHXXvQGqu
Set cpOZYZZPXLGMDYz = CreateObject("scripting.filesystemobject")
Set FrSOMeoATRynaIe = cpOZYZZPXLGMDYz.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-V2tKElwscZWlTcQNykjwbgaFFvJuNxF7.tmp", True)
Set UiSVfZeHXXvQGqu = cpOZYZZPXLGMDYz.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-aZ6CrmeYjfyFFpHpb7NJSscLXuHet4IK.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Const HKEY = &H80000002

Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue

newValue = " -startall"
outFile = "E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-fUr66SfPe3RLcFdVr5Kmv6aP4guiQQyN.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

objRegistry.SetExpandedStringValue HKEY, "SYSTEM\CurrentControlSet\Services\bearsamppxlight\Parameters", "AppParameters", newValue
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Services\bearsamppxlight\Parameters\AppParameters")
    If entryValue = newValue Then
        objFile.Write "REG_NO_ERROR"
    Else
        objFile.Write "REG_ERROR_SET" & newValue
    End If
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
FrSOMeoATRynaIe.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
FrSOMeoATRynaIe.Write Err.Description
End If
UiSVfZeHXXvQGqu.Write "FINISHED!"
UiSVfZeHXXvQGqu.Close
FrSOMeoATRynaIe.Close
