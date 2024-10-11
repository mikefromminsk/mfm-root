cd ..
for /d %%d in (mfm*) do (
    cd %%d
    npm publish
    cd ..
)