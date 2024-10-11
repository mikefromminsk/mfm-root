cd ..
for /d %%d in (mfm*) do (
    cd %%d
    npm publish
    cd ..
    set /p DUMMY=Hit ENTER to continue...
)