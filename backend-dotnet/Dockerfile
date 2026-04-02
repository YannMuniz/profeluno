    # Acesse https://aka.ms/customizecontainer para saber como personalizar seu contêiner de depuração e como o Visual Studio usa este Dockerfile para criar suas imagens para uma depuração mais rápida.

    # Esta fase é usada durante a execução no VS no modo rápido (Padrão para a configuração de Depuração)
    FROM mcr.microsoft.com/dotnet/aspnet:8.0 AS base
    USER $APP_UID
    WORKDIR /app
    EXPOSE 9000

    ENV ASPNETCORE_HTTP_PORTS=9000


    # Esta fase é usada para compilar o projeto de serviço
    # FROM mcr.microsoft.com/dotnet/sdk:8.0 AS build
    FROM mcr.microsoft.com/dotnet/sdk@sha256:a9330090b730c2abde8f3f43b3d1e24e4b8cba028de7bab1a7fdcd50cb7a3b7e AS build
    ARG BUILD_CONFIGURATION=Release
    WORKDIR /src
    COPY ["Profeluno.csproj", "."]
    RUN dotnet restore "./Profeluno.csproj"
    COPY . .
    WORKDIR "/src/."
    RUN dotnet build "./Profeluno.csproj" -c $BUILD_CONFIGURATION -o /app/build

    # Esta fase é usada para publicar o projeto de serviço a ser copiado para a fase final
    FROM build AS publish
    ARG BUILD_CONFIGURATION=Release
    RUN dotnet publish "./Profeluno.csproj" -c $BUILD_CONFIGURATION -o /app/publish /p:UseAppHost=false

    # Esta fase é usada na produção ou quando executada no VS no modo normal (padrão quando não está usando a configuração de Depuração)
    FROM base AS final
    WORKDIR /app
    COPY --from=publish /app/publish .
    ENTRYPOINT ["dotnet", "Profeluno.dll"]