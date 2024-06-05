!macro CustomCodePreInstall
	${If} ${FileExists} "$INSTDIR\App\AppInfo\appinfo.ini"
		ReadINIStr $0 "$INSTDIR\App\AppInfo\appinfo.ini" "Version" "PackageVersion"
		${VersionCompare} $0 "3.0.0.0" $R0
		${If} $R0 == 2
		${AndIf} ${FileExists} "$INSTDIR\Data\settings\PWGen.ini"
			Rename "$INSTDIR\Data\settings\PWGen.ini" "$INSTDIR\Data\settings\PWTech.ini"
		${EndIf}
	${EndIf}
!macroend
