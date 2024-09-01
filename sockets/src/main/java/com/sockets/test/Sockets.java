package com.sockets.test;/*
 * Copyright (c) 2010-2020 Nathan Rajlich
 *
 *  Permission is hereby granted, free of charge, to any person
 *  obtaining a copy of this software and associated documentation
 *  files (the "Software"), to deal in the Software without
 *  restriction, including without limitation the rights to use,
 *  copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the
 *  Software is furnished to do so, subject to the following
 *  conditions:
 *
 *  The above copyright notice and this permission notice shall be
 *  included in all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 *  EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 *  OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 *  NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 *  HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 *  WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 *  FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 *  OTHER DEALINGS IN THE SOFTWARE.
 */

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.net.UnknownHostException;
import java.util.*;
import java.util.stream.Collectors;

import com.google.gson.Gson;
import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import com.sun.net.httpserver.HttpServer;
import com.sockets.test.model.Message;
import com.sockets.test.model.Subscription;
import org.java_websocket.WebSocket;
import org.java_websocket.handshake.ClientHandshake;
import org.java_websocket.server.WebSocketServer;

public class Sockets extends WebSocketServer {

    static Gson json = new Gson();
    static Map<String, HashSet<WebSocket>> channels = new HashMap<>();

    public Sockets(int port) throws UnknownHostException, IOException {
        super(new InetSocketAddress(port));

        HttpServer server = HttpServer.create(new InetSocketAddress(8002), 0);
        server.createContext("/test", new MyHandler());
        server.setExecutor(null);
        server.start();
        log("Http started on port: 8002");
    }

    public static void main(String[] args) throws UnknownHostException, IOException {
        new Sockets(8887).start();
        log("WS started on port: 8887");
    }

    public static void log(String message) {
        System.out.println(message);
    }


    static class MyHandler implements HttpHandler {
        @Override
        public void handle(HttpExchange t) throws IOException {
            String requestBody = new BufferedReader(new InputStreamReader(t.getRequestBody()))
                    .lines().collect(Collectors.joining("\n"));

            Message message = json.fromJson(requestBody, Message.class);
            if (channels.containsKey(message.channel)) {
                Iterator<WebSocket> iterator = channels.get(message.channel).iterator();
                while (iterator.hasNext()) {
                    WebSocket conn = iterator.next();
                    if (conn.isOpen()) {
                        conn.send(requestBody);
                    } else {
                        iterator.remove();
                    }
                }
            }
            log("channel: " + message.channel);
            String response = "This is the response";
            t.sendResponseHeaders(200, response.length());
            OutputStream os = t.getResponseBody();
            os.write(response.getBytes());
            os.close();
        }
    }

    int connections = 0;

    @Override
    public void onOpen(WebSocket conn, ClientHandshake handshake) {
        connections++;
        log(connections + " connected");
    }

    @Override
    public void onClose(WebSocket conn, int code, String reason, boolean remote) {
        connections--;
        log(connections + " connected");
    }

    @Override
    public void onMessage(WebSocket conn, String message) {
        Subscription subscription = json.fromJson(message, Subscription.class);
        if (!channels.containsKey(subscription.channel)) {
            channels.put(subscription.channel, new HashSet<>());
        }
        channels.get(subscription.channel).add(conn);
    }

    @Override
    public void onError(WebSocket conn, Exception ex) {
        ex.printStackTrace();
        if (conn != null) {
            // some errors like port binding failed may not be assignable to a specific websocket
        }
    }

    @Override
    public void onStart() {
        log("Server started!");
        setConnectionLostTimeout(0);
        setConnectionLostTimeout(100);
    }

}
