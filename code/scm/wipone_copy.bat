@echo off
net use /delete \\192.168.6.222\po
net use M: \\192.168.6.222\po /user:administrator carnival@@27113171
xcopy D:\mode\wipone\*.xls M:\ /d/Y
if errorlevel 0 goto ok
goto done
:ok
move D:\mode\wipone\*.xls D:\mode\wipone_copied\ 
goto done
:done
net use /delete \\192.168.6.222\po