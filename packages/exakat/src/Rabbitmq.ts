import * as amqp from "amqplib";

export class RabbitMQ {
  private connection: amqp.Connection | undefined;
  private channel: amqp.Channel | undefined;
  private static readonly queue: string = "exakat";
  private exchange: string;
  private routingKey: string;
  public static instance: RabbitMQ | undefined;

  public static getInstance(): RabbitMQ {
    if (RabbitMQ.instance === undefined) {
      RabbitMQ.instance = new RabbitMQ("exakat", "exakat");
    }
    return RabbitMQ.instance;
  }

  private constructor(exchange: string, routingKey: string) {
    this.exchange = exchange;
    this.routingKey = routingKey;
  }

  public async connect(): Promise<void> {
    this.connection = await amqp.connect("amqp://localhost");
    this.channel = await this.connection.createChannel();
    await this.channel.assertQueue(RabbitMQ.queue, { durable: true });
    await this.channel.assertExchange(this.exchange, "direct", {
      durable: true,
    });
    await this.channel.bindQueue(
      RabbitMQ.queue,
      this.exchange,
      this.routingKey
    );
  }

  public async send(message: string): Promise<void> {
    if (this.channel === undefined) {
      throw new Error("Channel is undefined");
    }
    this.channel.publish(this.exchange, this.routingKey, Buffer.from(message));
  }

  public async close(): Promise<void> {
    if (this.channel === undefined) {
      throw new Error("Channel is undefined");
    }
    if (this.connection === undefined) {
      throw new Error("Connection is undefined");
    }
    await this.channel.close();
    await this.connection.close();
  }

  public async consume(
    callback: (msg: amqp.ConsumeMessage | null) => void
  ): Promise<void> {
    if (this.channel === undefined) {
      throw new Error("Channel is undefined");
    }
    this.channel.consume(RabbitMQ.queue, callback, { noAck: true });
  }

  public static convertMessageToString(
    msg: amqp.ConsumeMessage | null
  ): string {
    if (msg === null) {
      throw new Error("Message is null");
    }
    return msg.content.toString();
  }
}
