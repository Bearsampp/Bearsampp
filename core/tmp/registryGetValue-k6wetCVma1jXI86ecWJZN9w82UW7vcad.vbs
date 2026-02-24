On Error Resume Next
Dim bxlFKjwNuUhbkXy, npCscSQKgcUibJe, dqsKemwCSpSgYia, YLxNwJHnZMHkaph
Set npCscSQKgcUibJe = CreateObject("scripting.filesystemobject")
Set dqsKemwCSpSgYia = npCscSQKgcUibJe.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-kWiGxMFRrHVzx2JrrwPmyMympcdEPi6f.tmp", True)
Set YLxNwJHnZMHkaph = npCscSQKgcUibJe.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-19rVSpoFU0I2jS34Ue7Can0hOBg5S1Az.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Dim objShell, objFso, objFile, outFile, entryValue

outFile = "E:\Bearsampp-development\sandbox\core\tmp\registryGetValue-noCydlMofhBo972FQsYve3HXZRwvKSnK.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment\Path")
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    objFile.Write entryValue
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
dqsKemwCSpSgYia.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
dqsKemwCSpSgYia.Write Err.Description
End If
YLxNwJHnZMHkaph.Write "FINISHED!"
YLxNwJHnZMHkaph.Close
dqsKemwCSpSgYia.Close
