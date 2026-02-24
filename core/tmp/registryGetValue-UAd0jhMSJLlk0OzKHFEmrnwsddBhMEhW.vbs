On Error Resume Next
Dim UTpHHgRcZtZfgLW, GOWwWXUgsKKkGDh, BACBwjCPCnJSofb, exPkRVofcosdXbB
Set GOWwWXUgsKKkGDh = CreateObject("scripting.filesystemobject")
Set BACBwjCPCnJSofb = GOWwWXUgsKKkGDh.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-9pDbemCNW4g8dW3J9jh6V00gpIfvs4ln.tmp", True)
Set exPkRVofcosdXbB = GOWwWXUgsKKkGDh.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-TdfgfXiWNbx1ukcnWjgAY5ml6stfk2lJ.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, entryValue

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-PGWxGvBcYOKqnWMYFcuOq9K3PtcsdD8F.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment\BEARSAMPP_BINS")
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    objFile.Write entryValue
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
BACBwjCPCnJSofb.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
BACBwjCPCnJSofb.Write Err.Description
End If
exPkRVofcosdXbB.Write "FINISHED!"
exPkRVofcosdXbB.Close
BACBwjCPCnJSofb.Close
