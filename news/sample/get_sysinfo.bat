@echo off
REM 20171114 0835
REM utf8


REM ===========================
REM ftp setting
set UploadFG=1
set Web=http://120.116.25.31:8080/x259/modules/info_whats/
set key=info_whats
REM ===========================

set UploadPage=%Web%comp_on.php
set NewVersion=%Web%news/
set RealIpPage=%Web%yourip.php





SET now_path=%~dp0
REM echo %now_path%
cd %now_path%

set hour=%time:~0,2%
if "%hour:~0,1%" == " " set hour=0%hour:~1,1%
REM echo hour=%hour%
set min=%time:~3,2%
if "%min:~0,1%" == " " set min=0%min:~1,1%
REM echo min=%min%
set secs=%time:~6,2%
if "%secs:~0,1%" == " " set secs=0%secs:~1,1%
REM echo secs=%secs%

set mydate=%date:~0,4%%date:~5,2%%date:~8,2%_%hour%%min%%secs%

REM  echo %mydate%

set file=%mydate%.txt

REM chcp 65001
REM wmic os get localdatetime > %file%

REM wmic csproduct get UUID
wmic csproduct get UUID >> %file%

REM wmic bios get name
wmic bios get biosversion , name >> %file%

REM wmic cpu get name
wmic cpu get name >> %file%

REM  wmic MEMPHYSICAL get maxcapacity
wmic MEMPHYSICAL get maxcapacity >> %file%

REM wmic NICCONFIG   get dhcpserver
wmic NICCONFIG   get dhcpserver   >> %file%

wmic ComputerSystem get TotalPhysicalMemory >> %file%

wmic baseboard get product,Manufacturer,version,serialnumber >> %file%


REM wmic NICCONFIG   get IPAddress
wmic NICCONFIG   get IPAddress , macaddress >> %file%


REM wmic product get name >> %file%

%now_path%curl.exe -4 %RealIpPage% -o ip.txt  --silent

type ip.txt >> %file%

del ip.txt 


REM copy the first info
IF  EXIST FIRST_SYSTEM_INFO GOTO step2
copy %file% FIRST_SYSTEM_INFO



:step2

if %UploadFG% EQU 1 (
   %now_path%curl -X POST -F "do=%key%" -F "uploaded=@%file%"  %UploadPage% -silent
)

cscript %now_path%compare_txt.vbs %file%  FIRST_SYSTEM_INFO

del %file%

REM  auto DownLoad new version get_sysinfo.bat


REM compare_txt

REM %now_path%curl.exe  --fail -O  %NewVersion%compare_txt.vbs   --silent



REM %now_path%curl.exe  --fail  -O  %NewVersion%get_sysinfo.bat   --silent




























