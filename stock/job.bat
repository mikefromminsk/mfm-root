@echo off
cd c:\wamp\www\
for /l %%i in (1,1,150) do (
    c:\wamp\bin\php\php5.6.40\php.exe -f c:\wamp\www\stock\api\job10s.php
    timeout 2
)
