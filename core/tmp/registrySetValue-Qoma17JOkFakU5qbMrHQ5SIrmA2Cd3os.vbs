On Error Resume Next
Dim fLKPtfjlJCtWxJa, LYLKTKaUWLeOTbX, SIyKzvVvZCdGeJk, UJelCKtbCKZNcoG
Set LYLKTKaUWLeOTbX = CreateObject("scripting.filesystemobject")
Set SIyKzvVvZCdGeJk = LYLKTKaUWLeOTbX.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-nJQdprL4EMf7mNNWNOhgZFs9QIUAzCsc.tmp", True)
Set UJelCKtbCKZNcoG = LYLKTKaUWLeOTbX.CreateTextFile("E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-FdTj0XKjbDFvR7qnESQP3hNlGOgjCgxc.tmp", True)
startTime = Timer
timeoutSeconds = 10

On Error Resume Next
Err.Clear

Const HKEY = &H80000002

Dim objShell, objRegistry, objFso, objFile, outFile, entryValue, newValue

newValue = "%BEARSAMPP_BINS%;C:\Program Files\Microsoft\jdk-25.0.1.8-hotspot\bin;E:\Bearsampp-development\sandbox\bin;E:\ProgramFiles\delphi\bin;C:\Users\Public\Documents\Embarcadero\Studio\23.0\Bpl;E:\ProgramFiles\delphi\bin64;C:\Users\Public\Documents\Embarcadero\Studio\23.0\Bpl\Win64;c:\windows;c:\windows\system32;C:\Program Files (x86)\Razer Chroma SDK\bin;C:\Program Files\Razer Chroma SDK\bin;C:\Program Files\Common Files\Oracle\Java\javapath;C:\Program Files (x86)\Razer\ChromaBroadcast\bin;C:\Program Files\Razer\ChromaBroadcast\bin;c:\program files\microsoft\jdk-17.0.1.12-hotspot\bin;c:\windows\system32\wbem;c:\windows\system32\windowspowershell\v1.0;c:\windows\system32\openssh;c:\program files\microsoft sql server\150\tools\binn;c:\program files\putty;C:\Program Files (x86)\NVIDIA Corporation\PhysX\Common;C:\ProgramData\chocolatey\bin;C:\Program Files (x86)\WinMerge;C:\Program Files (x86)\gsudo;C:\Users\troy\AppData\Local\Microsoft\WindowsApps;C:\Users\troy\AppData\Local\JetBrains\Toolbox\scripts;C:\Users\troy\.dotnet\tools;E:\ProgramFiles\Microsoft VS Code\bin;C:\Program Files\GitHub CLI;C:\Program Files\Notepad++;C:\Program Files\Microsoft SQL Server\110\Tools\Binn;C:\Program Files (x86)\Microsoft Visual Studio\2022\BuildTools\VC\Tools\MSVC\14.16.27023\bin\HostX64\x86;C:\WINDOWS\system32\config\systemprofile\AppData\Local\Microsoft\WindowsApps;C:\Users\troy\AppData\Local\Programs\oh-my-posh\bin;E:\ProgramFiles\doxygen\bin;C:\WINDOWS\system32;C:\WINDOWS;C:\WINDOWS\System32\Wbem;C:\WINDOWS\System32\WindowsPowerShell\v1.0;C:\WINDOWS\System32\OpenSSH;C:\Program Files\NVIDIA Corporation\NVIDIA app\NvDLISR;C:\Program Files\Vagrant\bin;E:\ProgramFiles\apache-ant\bin;C:\Program Files\dotnet;C:\Program Files\PowerShell\7;C:\Users\troy\.dnx\bin;C:\Program Files\Microsoft DNX\Dnvm;C:\Program Files\Microsoft SQL Server\130\Tools\Binn;C:\Program Files (x86)\Windows Kits\10\Windows Performance Toolkit;%SystemRoot%\system32;%SystemRoot%;%SystemRoot%\System32\Wbem;%SYSTEMROOT%\System32\WindowsPowerShell\v1.0\;%SYSTEMROOT%\System32\OpenSSH\;C:\Program Files\PowerShell\7\;C:\Program Files\Git\cmd;C:\Program Files\dotnet\"
outFile = "E:\Bearsampp-development\sandbox\core\tmp\registrySetValue-Z7AkMBmpRehiV8wIe7JP3KfP1njucVbd.vbs"
Set objShell = WScript.CreateObject("WScript.Shell")
Set objRegistry = GetObject("winmgmts://./root/default:StdRegProv")
Set objFso = CreateObject("Scripting.FileSystemObject")
Set objFile = objFso.CreateTextFile(outFile, True)

objRegistry.SetExpandedStringValue HKEY, "SYSTEM\CurrentControlSet\Control\Session Manager\Environment", "Path", newValue
If Err.Number <> 0 Then
    objFile.Write "REG_ERROR_ENTRY" & Err.Number & ": " & Err.Description
Else
    entryValue = objShell.RegRead("HKLM\SYSTEM\CurrentControlSet\Control\Session Manager\Environment\Path")
    If entryValue = newValue Then
        objFile.Write "REG_NO_ERROR"
    Else
        objFile.Write "REG_ERROR_SET" & newValue
    End If
End If
objFile.Close


If Timer - startTime > timeoutSeconds Then
SIyKzvVvZCdGeJk.Write "VBScript execution timed out after " & timeoutSeconds & " seconds"
End If
If Err.Number <> 0 Then
SIyKzvVvZCdGeJk.Write Err.Description
End If
UJelCKtbCKZNcoG.Write "FINISHED!"
UJelCKtbCKZNcoG.Close
SIyKzvVvZCdGeJk.Close
