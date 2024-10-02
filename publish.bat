REM Поиск всех папок с префиксом mfm
for /d %%d in (mfm*) do (
    REM Публикация пакета %%d
    cd %%d
    npm publish
    cd ..
)

pause