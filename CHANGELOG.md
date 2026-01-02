# Changelog

## v1.1.2 – 2026-01-01

### Added
- Publicação automática de assets via Composer (`post-install` e `post-update`)
- Diretório público padrão `/ms` criado automaticamente
- Script `ms-install.php` disponível como binário opcional

### Changed
- MS Framework não depende mais de helpers, URLs ou contexto HTTP
- Processo de instalação agora é 100% compatível com CLI e hospedagem compartilhada

### Fixed
- Falhas em ambientes sem `HTTP_HOST`
- Erros de execução do Composer em projetos que usam autoload por arquivos
- Problemas de paths relativos em instalações locais e produção

