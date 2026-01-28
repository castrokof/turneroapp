package com.turnero.tv;

import android.app.Activity;
import android.os.Bundle;
import android.view.View;
import android.view.WindowManager;
import android.webkit.WebChromeClient;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.EditText;
import android.widget.Button;
import android.widget.LinearLayout;
import android.content.SharedPreferences;

public class MainActivity extends Activity {

    private WebView webView;
    private LinearLayout configLayout;
    private EditText urlInput;
    private String savedUrl;
    private static final String PREFS_NAME = "TurneroPrefs";
    private static final String URL_KEY = "tv_url";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Pantalla completa y mantener encendida
        getWindow().setFlags(
            WindowManager.LayoutParams.FLAG_FULLSCREEN,
            WindowManager.LayoutParams.FLAG_FULLSCREEN
        );
        getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webView);
        configLayout = findViewById(R.id.configLayout);
        urlInput = findViewById(R.id.urlInput);
        Button btnConnect = findViewById(R.id.btnConnect);

        // Cargar URL guardada
        SharedPreferences prefs = getSharedPreferences(PREFS_NAME, MODE_PRIVATE);
        savedUrl = prefs.getString(URL_KEY, "");

        if (!savedUrl.isEmpty()) {
            urlInput.setText(savedUrl);
            loadUrl(savedUrl);
        }

        btnConnect.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String url = urlInput.getText().toString().trim();
                if (!url.isEmpty()) {
                    // Asegurar que tiene el path /display/tv
                    if (!url.endsWith("/display/tv")) {
                        if (url.endsWith("/")) {
                            url = url + "display/tv";
                        } else {
                            url = url + "/display/tv";
                        }
                    }

                    // Guardar URL
                    SharedPreferences.Editor editor = getSharedPreferences(PREFS_NAME, MODE_PRIVATE).edit();
                    editor.putString(URL_KEY, url);
                    editor.apply();

                    loadUrl(url);
                }
            }
        });

        configureWebView();
    }

    private void configureWebView() {
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setMediaPlaybackRequiresUserGesture(false); // Permitir audio sin interaccion
        settings.setCacheMode(WebSettings.LOAD_DEFAULT);
        settings.setAllowFileAccess(true);
        settings.setAllowContentAccess(true);
        settings.setLoadsImagesAutomatically(true);
        settings.setMixedContentMode(WebSettings.MIXED_CONTENT_ALWAYS_ALLOW);

        webView.setWebViewClient(new WebViewClient() {
            @Override
            public void onPageFinished(WebView view, String url) {
                super.onPageFinished(view, url);
                // Ocultar configuracion cuando carga la pagina
                configLayout.setVisibility(View.GONE);
                webView.setVisibility(View.VISIBLE);
            }

            @Override
            public void onReceivedError(WebView view, int errorCode, String description, String failingUrl) {
                super.onReceivedError(view, errorCode, description, failingUrl);
                // Mostrar configuracion si hay error
                configLayout.setVisibility(View.VISIBLE);
                webView.setVisibility(View.GONE);
            }
        });

        webView.setWebChromeClient(new WebChromeClient());
    }

    private void loadUrl(String url) {
        if (!url.startsWith("http://") && !url.startsWith("https://")) {
            url = "http://" + url;
        }
        webView.loadUrl(url);
    }

    @Override
    public void onBackPressed() {
        // Mostrar configuracion al presionar back
        if (configLayout.getVisibility() == View.GONE) {
            configLayout.setVisibility(View.VISIBLE);
            webView.setVisibility(View.GONE);
        } else {
            super.onBackPressed();
        }
    }

    @Override
    protected void onResume() {
        super.onResume();
        webView.onResume();
    }

    @Override
    protected void onPause() {
        super.onPause();
        webView.onPause();
    }
}
