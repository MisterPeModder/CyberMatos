# Setup a fully-featrued development environment for PHP + Ansible projects
{
  inputs = {
    devenv.url = "github:cachix/devenv";
    flake-utils.url = "github:numtide/flake-utils";
    nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
  };

  outputs = { self, devenv, flake-utils, nixpkgs, ... } @ inputs:
  flake-utils.lib.eachDefaultSystem (system:
  let
    pkgs = nixpkgs.legacyPackages.${system};
    php = pkgs.php82.buildEnv {
      extensions = { all, enabled }: with all; enabled ++ [ pkgs.php82.extensions.xdebug ];
      extraConfig = ''
        short_open_tag = off
        zend_extension = xdebug
        xdebug.mode = develop,coverage,debug,gcstats,profile,trace
      '';
    };

  in
  {
    devShells.default = devenv.lib.mkShell {
      inherit inputs pkgs;

      modules = [
        {
          packages = [
            pkgs.ansible-lint
            pkgs.symfony-cli
          ];

          languages.ansible.enable = true;

          languages.php = {
            enable = true;
            package = php;
            extensions = [ "xdebug" "jack" ];
          };

          services.mysql = {
            enable = true;
            package = pkgs.mariadb;
          };

          enterShell = ''
            export buildInputs="${php.packages.composer} $buildInputs"
          '';
        }
      ];
    };
  });
}
