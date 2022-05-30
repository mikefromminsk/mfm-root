@echo off
cd c:\wamp\www\
for /l %%i in (1,1,1150) do (
    c:\wamp\bin\php\php5.6.40\php.exe -f c:\wamp\www\stock\api\bot_spred.php
    timeout 2
)
