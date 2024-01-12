import { exec } from "child_process";
import * as util from "util";

const execPromesify = util.promisify(exec);

export class Exakat {
  public name: string;
  public id: string;
  public repositoryPath: string;
  public static readonly binaryPath = "/app/exakat/exakat.phar";

  constructor(name: string, id: string) {
    this.name = name;
    this.id = id;
    this.repositoryPath = "/app/repositories/" + this.name;
  }

  public async initProjects(): Promise<void> {
    const res = await execPromesify(
      `php ${Exakat.binaryPath} init -p ${this.name}${this.id} -copy ${this.repositoryPath} -v`
    );
    if (res.stderr) {
      throw new Error("Error while initializing the project" + res.stderr);
    }
  }

  public async analyzeProject(): Promise<void> {
    const res = await execPromesify(
      `php ${Exakat.binaryPath} project -p ${this.name}${this.id} -v`
    );
    if (res.stderr) {
      throw new Error("Error while analyzing the project" + res.stderr);
    }
  }

  public async reportProject(): Promise<void> {
    const res = await execPromesify(
      `php ${Exakat.binaryPath} report -p ${this.name}${this.id} -format Json -v`
    );
    if (res.stderr) {
      throw new Error("Error while creating the rapport" + res.stderr);
    }
  }
}
