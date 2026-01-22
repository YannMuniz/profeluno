FROM mcr.microsoft.com/dotnet/sdk:8.0

WORKDIR /app

COPY . .

# Instalar dotnet watch para hot reload
RUN dotnet tool install -g dotnet-watch

# Restaurar e compilar se houver projeto
RUN dotnet restore 2>/dev/null || true && \
    dotnet build 2>/dev/null || true

EXPOSE 5000

# Se não houver projeto, apenas esperar
CMD ["/bin/bash", "-c", "dotnet watch run --urls http://+:5000 2>/dev/null || tail -f /dev/null"]
