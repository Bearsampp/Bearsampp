On Error Resume Next
Dim cpAatfAuEjTwEXa, jvCOfwQnpUREIOL, qGMZiaZhEpqKbns, RLqkcCEqrcKRdMb
Set jvCOfwQnpUREIOL = CreateObject("scripting.filesystemobject")
Set qGMZiaZhEpqKbns = jvCOfwQnpUREIOL.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-NQax5ZXKxFqFV1CDXQnxAck4QTDDAmUg.tmp", True)
Set RLqkcCEqrcKRdMb = jvCOfwQnpUREIOL.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-0HkuTl7Jio5bqMRYyJCNe14TA4QPZRNP.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Const HKEY = &H80000002

Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue

newValue = " --listen ""127.0.0.1:8025"" --smtp ""127.0.0.1:25"" --webroot ""mail"""
outFile = "E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-ij4gh9KgP1I4ezdfJH0v0gNM2s8gvOuk.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

objRegistry.SetExpandedStringValue HKEY, "SYSTEM\CurrentControlSet\Services\bearsamppmailpit\Parameters", "AppParameters", newValue
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Services\bearsamppmailpit\Parameters\AppParameters")
    If entryValue = newValue Then
        objFile.Write "REG_NO_ERROR"
    Else
        objFile.Write "REG_ERROR_SET" & newValue
    End If
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
qGMZiaZhEpqKbns.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
qGMZiaZhEpqKbns.Write Err.Description
End If
RLqkcCEqrcKRdMb.Write "FINISHED!"
RLqkcCEqrcKRdMb.Close
qGMZiaZhEpqKbns.Close
